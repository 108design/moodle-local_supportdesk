// GPL-3.0-or-later; 108design, 2026-10-03. Only named Moodle AMD modules are built.
import fs from 'node:fs';
import path from 'node:path';
import babel from '@babel/core';
import {minify} from 'terser';
import {fileURLToPath} from 'node:url';
process.chdir(path.dirname(path.dirname(fileURLToPath(import.meta.url))));
const checking = process.argv.includes('--check');
let count = 0;
for (const name of fs.readdirSync('amd/src').filter(name => name.endsWith('.js'))) {
    const transformed = babel.transformSync(fs.readFileSync('amd/src/' + name, 'utf8'), {
        babelrc: false, configFile: false,
        plugins: [['@babel/plugin-transform-modules-amd', {moduleId: 'local_supportdesk/' + name.slice(0, -3)}]]
    });
    const built = (await minify(transformed.code, {format: {comments: false}})).code + '\n';
    const output = 'amd/build/' + name.replace('.js', '.min.js');
    if (checking && (!fs.existsSync(output) || fs.readFileSync(output, 'utf8') !== built)) throw new Error('Asset differs: ' + output);
    if (!checking) { fs.mkdirSync('amd/build', {recursive: true}); fs.writeFileSync(output, built); }
    count++;
}
console.log(`${checking ? 'Verified' : 'Built'} ${count} named Moodle AMD modules; no CSS framework.`);
