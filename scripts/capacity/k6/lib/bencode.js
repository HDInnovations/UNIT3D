// Decode raw tracker bytes; compact peers are not UTF-8 text.
export function bdecode(bytes) {
  let pos = 0;
  function ascii(start, end) {
    let value = '';
    for (let i = start; i < end; i++) value += String.fromCharCode(bytes[i]);
    return value;
  }
  function readInt(terminator) {
    const start = pos;
    while (pos < bytes.length && bytes[pos] !== terminator) pos++;
    if (pos === bytes.length) throw new Error('Truncated bencoded number.');
    const raw = ascii(start, pos++);
    if (!/^-?\d+$/.test(raw)) throw new Error('Invalid bencoded number.');
    return Number(raw);
  }
  function byteString() {
    const length = readInt(0x3a);
    if (!Number.isSafeInteger(length) || length < 0 || length > bytes.length - pos) {
      throw new Error('Truncated bencoded byte string.');
    }
    const value = bytes.subarray(pos, pos + length);
    pos += length;
    return value;
  }
  function value() {
    const token = bytes[pos];
    if (token === 0x69) {
      pos++;
      return readInt(0x65);
    }
    if (token === 0x6c || token === 0x64) {
      pos++;
      const result = token === 0x6c ? [] : {};
      while (bytes[pos] !== 0x65) {
        if (pos >= bytes.length) throw new Error('Truncated bencoded container.');
        if (token === 0x6c) result.push(value());
        else {
          const key = byteString();
          let name = '';
          for (let i = 0; i < key.length; i++) name += String.fromCharCode(key[i]);
          result[name] = value();
        }
      }
      pos++;
      return result;
    }
    if (token >= 0x30 && token <= 0x39) return byteString();
    throw new Error('Invalid bencoded value.');
  }
  return { value: value(), consumed: pos };
}
