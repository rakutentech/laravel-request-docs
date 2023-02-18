# Notes

### Local UI Development


```
npm install
npm run dev
```

#### Step 1

**Optional** Enable CORS on Laravel to allow localhost:3000
**Recommended** Open Chrome with `--disable-web-security` flag

On Mac to open chrome command:

```
open -n -a /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --args --user-data-dir='/tmp/chrome_dev_test' --ignore-certificate-errors --ignore-urlfetcher-cert-requests --disable-web-security
```



#### Step 2

**Open in Browser** http://localhost:3000?api=http://localhost:8000/request-docs/api


## Deployment notes

Handled by Github Actions or manually by @kevincobain2000

```
npm run export
```

Exports SSG production site to `../resources/dist/`, severed by Laravel


