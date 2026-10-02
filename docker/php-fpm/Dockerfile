# syntax=docker/dockerfile:1
#
# Adds PHP-FPM on top of the existing Sail CLI runtime image so the FPM,
# queue, schedule and Reverb workers all run identical PHP builds/extensions
# (including composer, the `cpx` backup CLI dependency, and every
# `php8.4-*` extension installed by the Sail recipe). Build `sail-runtime`
# with the build profile first, then build `laravel.test`; its named `sail`
# context resolves that local sail-8.4/app image. All HTTP and CLI workers
# subsequently use the resulting sail-8.4/fpm image.
FROM sail

USER root

RUN apt-get update \
    && apt-get install -y php8.4-fpm gettext-base \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Pools are rendered from templates at container start (see start-fpm) so
# `*_FPM_MAX_CHILDREN` etc. are tunable per environment without a rebuild.
# Drop the stock package-provided pool first so only our `web`/`tracker`
# pools (each with its own pm.status_listen) end up in pool.d.
RUN rm -f /etc/php/8.4/fpm/pool.d/*.conf
RUN mkdir -p /etc/php/8.4/fpm/pool.d.templates
COPY pool.d/web.conf.template /etc/php/8.4/fpm/pool.d.templates/web.conf.template
COPY pool.d/tracker.conf.template /etc/php/8.4/fpm/pool.d.templates/tracker.conf.template
# Sail configures uploads/memory only for CLI; preserve those limits for HTTP too.
RUN cp /etc/php/8.4/cli/conf.d/99-sail.ini /etc/php/8.4/fpm/conf.d/99-sail.ini
COPY opcache.ini /etc/php/8.4/fpm/conf.d/99-opcache.ini
COPY start-fpm /usr/local/bin/start-fpm

RUN chmod +x /usr/local/bin/start-fpm

# 9000 = web pool, 9001 = tracker pool, 9100/9101 = their private
# pm.status_listen status/ping listeners (never published to the host).
EXPOSE 9000 9001 9100 9101

ENTRYPOINT ["start-fpm"]
