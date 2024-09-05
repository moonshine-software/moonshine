import {promises as fsPromises, readFileSync, writeFileSync} from 'fs'

const moonShineBuildPlugin = () => ({
  name: 'moonshine-build-plugin',
  async closeBundle() {
    try {
      // prepend "(()=>{" to app.js
      const filePath = 'dist/assets/app.js'
      const data = readFileSync(filePath)
      const insert = Buffer.from('(()=>{')

      // Use writeFileSync without the third argument for encoding
      writeFileSync(filePath, insert)
      writeFileSync(filePath, data, {flag: 'a'}) // Use { flag: 'a' } for append mode

      // append "})()" to app.js
      await fsPromises.appendFile(filePath, '})()')
    } catch (e) {
      console.error(e)
    }
  },
})

export default moonShineBuildPlugin
