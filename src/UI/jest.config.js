export default {
  testEnvironment: 'jsdom',
  testEnvironmentOptions: {
    url: 'https://example.com',
  },
  transform: {},
  testPathIgnorePatterns: [
    '/coverage/',
    '/resources/js/__tests__/setup.js',
    '/resources/js/__tests__/__mocks__/',
  ],
  moduleFileExtensions: ['js', 'jsx', 'ts', 'tsx'],
  moduleNameMapper: {
    'follow-redirects': '<rootDir>/resources/js/__tests__/__mocks__/follow-redirects.js',
  },
  roots: ['resources/js'],
  collectCoverage: true,
  collectCoverageFrom: ['<rootDir>/resources/js/**/*.{js,ts,jsx,tsx}'],
  coverageDirectory: '<rootDir>/resources/js/__tests__/coverage',
  coveragePathIgnorePatterns: [
    '/resources/js/Components/',
    '/resources/js/Support/UI.js',
    '/resources/js/moonshine.js',
    '/resources/js/bootstrap.js',
    '/resources/js/app.js',
    '/resources/js/layout.js',
    '/resources/js/moonshine-build.js',
    '/resources/js/__tests__',
  ],
  setupFilesAfterEnv: ['<rootDir>/resources/js/__tests__/setup.js'],
}
