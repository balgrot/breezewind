module.exports = {
  content: [
    './*.php',
    './templates/**/*.php',
    './js/**/*.js',
    './node_modules/flowbite/**/*.js'

  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')
  ],
}