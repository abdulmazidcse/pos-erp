module.exports = {
  root: true,
  env: {
    node: true,
    browser: true,
  },
  extends: [
    'plugin:vue/vue3-essential', // or vue3-recommended
    'eslint:recommended',
  ],
  parserOptions: {
    ecmaVersion: 2020,
  },
  rules: {
    // Existing rules
    'no-dupe-keys': 'off',
    'vue/no-dupe-keys': 'off',
    'vue/no-deprecated-destroyed-lifecycle': 'off',

    'vue/require-v-for-key': 'off',
    'vue/no-unused-vars': 'warn',
    'vue/no-use-v-if-with-v-for': 'off',
    'vue/multi-word-component-names': 'off',

    'no-cond-assign': 'off',
    'no-unused-vars': 'warn',

    // Your new requested ignores:
    'vue/no-parsing-error': 'off',           // Ignore parsing errors like missing semicolons in templates
    'vue/no-unused-components': 'off',       // Ignore warnings about registered-but-unused components
    'no-redeclare': 'off',                    // Turn off redeclaration errors (like 'postEvent' already defined)
    'no-empty': 'off',     
    'no-mixed-spaces-and-tabs':'off',               // Turn off redeclaration errors (like 'postEvent' already defined)

    // Console/debugger rules
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
  },
};

