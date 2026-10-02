import http from 'k6/http';
import { check } from 'k6';
import { parseHTML } from 'k6/html';

const BASE_URL = __ENV.CAPACITY_BASE_URL || 'https://capacity-nginx';

// Real Fortify session login: GET /login for the CSRF cookie+token, then
// POST username/password, matching an actual browser instead of forging a
// session.
//
// Fortify's login response is ALWAYS a redirect, success or failure: on
// success it redirects to the intended/home URL; on failure it redirects
// back to `/login` with validation errors flashed to the session. A plain
// `status === 200` (or a followed-redirect's final 200) can NOT distinguish
// these -- both a real authenticated page AND a re-rendered login form
// return 200. So this does NOT follow the redirect (`redirects: 0`) and
// instead asserts the raw POST response is itself a 3xx whose `Location`
// does NOT point back at `/login`.
export function login(jar, username, password, headers) {
  const getRes = http.get(`${BASE_URL}/login`, { jar, headers, tags: { name: 'login_get' } });
  const form = {};
  const inputs = parseHTML(getRes.body).find('form input[name]');
  inputs.each((index) => {
    const input = inputs.eq(index);
    if (input.attr('type') !== 'checkbox') form[input.attr('name')] = input.attr('value') || '';
  });
  const token = form._token;

  check(getRes, {
    'login page has csrf token': () => !!token,
  });

  if (!token) return false;

  const postRes = http.post(
    `${BASE_URL}/login`,
    { ...form, username, password },
    { jar, headers, redirects: 0, tags: { name: 'login_post' } }
  );

  const location = postRes.headers['Location'] || '';
  const redirectedAwayFromLogin = postRes.status >= 300 && postRes.status < 400 && !location.includes('/login');

  return check(postRes, {
    'login actually redirected away from /login (not a re-rendered failed-login form)': () => redirectedAwayFromLogin,
  });
}
