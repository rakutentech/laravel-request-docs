# Notes

## Local UI Development

nodejs >= 16

```
cd ui/
npm install
npm run dev
```

## 1) Mode: Readonly

**Open in Browser** 

http://localhost:3000/request-docs?api=http://localhost:3000/request-docs/sample.json


## 2) Mode: Developing with Laravel via npm

**Step 1**

**Optional** Enable CORS on Laravel to allow localhost:3000/request-docs

**Recommended** Open Chrome with `--disable-web-security` flag

On Mac to open chrome command:

```
open -n -a /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --args --user-data-dir='/tmp/chrome_dev_test' --ignore-certificate-errors --ignore-urlfetcher-cert-requests --disable-web-security
```


**Step 2**

**Open in Browser** http://localhost:3000/request-docs?api=http://localhost:8000/request-docs/api


## 3) Mode: Developing with Laravel via Laravel



Add to composer.json

```sh
    "repositories": [
        {
            "type": "path",
            "url": "/Users/yourpath/to/laravel-request-docs"
        }
    ],

```

```sh
composer require rakutentech/laravel-request-docs @dev
```

```
cd ui
npm run export
```

**Open in Browser** http://localhost:8000/request-docs



---

## Deployment notes

Handled by Github Actions or manually by @kevincobain2000

```
npm run export
```

Exports SSG production site to `../resources/dist/`, severed by Laravel


