export function getAPIInfoId(info?: IAPIInfo) {
  return info ? `${info.httpMethod}-${info.uri}`: ""
}