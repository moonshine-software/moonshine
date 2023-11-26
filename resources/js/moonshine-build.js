const fs = require('fs/promises')
const fsSync = require('fs')

const moonshineBuildPlugin = () => ({
    name: 'moonshine-build-plugin',
    async closeBundle()
    {
        try {
            //prepend "(()=>{" to app.js
            const data = fsSync.readFileSync('public/assets/app.js')
            const fd = fsSync.openSync('public/assets/app.js', 'w+')
            const insert = Buffer.from("(()=>{")
            fsSync.writeSync(fd, insert, 0, insert.length, 0)
            fsSync.writeSync(fd, data, 0, data.length, insert.length)
            fsSync.close(fd, (err) => {
                if (err) {
                    console.log('prepend to app.js file error', err);
                    throw err;
                }
            });

            //append "})()" to app.js
            await fs.appendFile('public/assets/app.js', '})()', function (err) {
                console.log('append to app.js file error', err);
            });
        } catch (e) {
            console.error('moonshine-build-plugin - closeBundle error:', e)
        }
    }

})

export default moonshineBuildPlugin;