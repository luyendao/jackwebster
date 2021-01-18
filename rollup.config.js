import babel from 'rollup-plugin-babel';
import { terser } from 'rollup-plugin-terser';
import resolve from 'rollup-plugin-node-resolve';
import livereload from 'rollup-plugin-livereload';
import { uglify } from 'rollup-plugin-uglify';
import postcss from 'rollup-plugin-postcss';

export default {
    input: `src/js/index.js`,
    output: {
        file: `bundle.js`,
        format: `iife`,
    },
    plugins: [
        process.env.NODE_ENV === 'development' && livereload({ watch: ['src/js/**/*', 'src/scss/**/*'] }),
        postcss({
            extract: 'style.min.css',
            minimize: true,
        }),
        resolve({
            browser: true,
        }),
        babel({
            exclude: 'node_modules/**',
            externalHelpers: true,
        }),
        process.env.NODE_ENV === 'production' && terser(),
        process.env.NODE_ENV === 'production' && uglify(),
    ],
}
