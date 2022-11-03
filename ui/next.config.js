/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  swcMinify: true,
  // for --turbo, remove basePath below
  images: {
    unoptimized: true
  },
  basePath: '/vendor/request-docs',
}

module.exports = nextConfig
