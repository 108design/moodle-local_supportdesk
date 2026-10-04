// GPL-3.0-or-later; 108design, 2026-10-03. Only named Moodle AMD modules are built.
import fs from 'node:fs';
import path from 'node:path';
import babel from '@babel/core';
import {minify} from 'terser';
import {fileURLToPath} from 'node:url';
process.chdir(path.dirname(path.dirname(fileURLToPath(import.meta.url))));
const checking = process.argv.includes('--check');
let count = 0;
const sources = fs.readdirSync('amd/src').filter(name => name.endsWith('.js')).map(name => [name, 'amd/src/' + name, '']);
sources.push(['photoswipe_core.js', 'thirdparty/photoswipe/photoswipe.esm.js', '/*! PhotoSwipe 5.4.4; MIT; thirdparty/photoswipe/LICENSE */\n'],
    ['photoswipe_lightbox.js', 'thirdparty/photoswipe/photoswipe-lightbox.esm.js', '/*! PhotoSwipe 5.4.4; MIT; thirdparty/photoswipe/LICENSE */\n']);
for (const [name, input, notice] of sources) {
    const transformed = babel.transformSync(fs.readFileSync(input, 'utf8'), {
        babelrc: false, configFile: false,
        plugins: [['@babel/plugin-transform-modules-amd', {moduleId: 'local_supportdesk/' + name.slice(0, -3)}]]
    });
    const built = notice + (await minify(transformed.code, {format: {comments: false}})).code + '\n';
    const output = 'amd/build/' + name.replace('.js', '.min.js');
    if (checking && (!fs.existsSync(output) || fs.readFileSync(output, 'utf8') !== built)) throw new Error('Asset differs: ' + output);
    if (!checking) { fs.mkdirSync('amd/build', {recursive: true}); fs.writeFileSync(output, built); }
    count++;
}
console.log(`${checking ? 'Verified' : 'Built'} ${count} named Moodle AMD modules; no CSS framework.`);
