/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 * File documentation for tools/build.mjs.
 *
 * @package   local_supportdesk
 * @copyright 2025 learn-ix support@learn-ix.com
 */
// GPL-3.0-or-later; 108design, 2026-10-03. Named Moodle AMD modules with real source maps.
import fs from 'node:fs';
import path from 'node:path';
import babel from '@babel/core';
import {minify} from 'terser';
import {fileURLToPath} from 'node:url';
import crypto from 'node:crypto';
process.chdir(path.dirname(path.dirname(fileURLToPath(import.meta.url))));
const checking = process.argv.includes('--check');
let count = 0;
const sources = fs.readdirSync('amd/src').filter(name => name.endsWith('.js')).map(name => [name, 'amd/src/' + name]);
sources.push(['photoswipe_core.js', 'thirdparty/photoswipe/photoswipe.esm.js'],
    ['photoswipe_lightbox.js', 'thirdparty/photoswipe/photoswipe-lightbox.esm.js']);
for (const [name, input] of sources) {
    const defaultReturn = ({types:t}) => ({
        pre(file) {
            this.hasDefault=file.ast.program.body.some(n=>n.type==='ExportDefaultDeclaration');
            this.needsRequire=false;
            file.path.traverse({ReferencedIdentifier:p=>{
                if (p.node.name==='require' && !p.scope.hasBinding('require')) this.needsRequire=true;
            }});
        },
        post(file) {
            const define=file.ast.program.body.find(n=>n.type==='ExpressionStatement' && n.expression.type==='CallExpression'
                && n.expression.callee.name==='define')?.expression;
            const factory=define?.arguments.at(-1);
            if (this.needsRequire && factory) {
                const dependencies=define.arguments.find(n=>n.type==='ArrayExpression');
                if (!dependencies.elements.some(n=>n.value==='require')) {
                    dependencies.elements.unshift(t.stringLiteral('require'));factory.params.unshift(t.identifier('require'));
                }
            }
            if (!this.hasDefault) return;
            const dependencies=define.arguments.find(n=>n.type==='ArrayExpression');
            const index=dependencies.elements.findIndex(n=>n.value==='exports');
            if (!factory?.params[index]) throw new Error('Missing default export binding');
            factory.body.body.push(t.returnStatement(t.memberExpression(factory.params[index],t.identifier('default'))));
        },
    });
    const module = name.slice(0,-3);
    const sourceName = input.startsWith('amd/src/') ? '../src/' + name : '../' + input;
    const transformed = babel.transformSync(fs.readFileSync(input,'utf8'), {
        babelrc:false, configFile:false, sourceMaps:true, filename:name, sourceFileName:sourceName,
        moduleIds:true, moduleId:'local_supportdesk/'+module,
        plugins:[['@babel/plugin-transform-modules-amd'],defaultReturn],
    });
    const filename=name.replace('.js','.min.js');
    const result=await minify(transformed.code,{ecma:2019,format:{comments:/^!|@license|@preserve/},
        sourceMap:{content:transformed.map,filename,url:filename+'.map',includeSources:true}});
    const code=result.code.trimEnd()+'\n';
    const map=JSON.stringify({...JSON.parse(result.map),x_108design_code_sha256_lf:
        crypto.createHash('sha256').update(code.replaceAll('\r\n','\n')).digest('hex')})+'\n';
    for (const [output,content] of [['amd/build/'+filename,code],['amd/build/'+filename+'.map',map]]) {
        if (checking && (!fs.existsSync(output) || fs.readFileSync(output,'utf8')!==content)) throw new Error('Asset differs: '+output);
        if (!checking) {fs.mkdirSync('amd/build',{recursive:true});fs.writeFileSync(output,content);}
    }
    count++;
}
console.log(`${checking?'Verified':'Built'} ${count} named AMD/map pairs; no CSS framework.`);
