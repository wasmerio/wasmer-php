#!/usr/bin/env node

const fs = require('fs');

WebAssembly
    .instantiate(fs.readFileSync('toy.wasm'), {})
    .then(result => result.instance.exports.sum(1, 2))
    .then(console.log);
