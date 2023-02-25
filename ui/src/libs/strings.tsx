
export const defaultHeaders = `{
    "Content-Type": "application/json",
    "Accept": "application/json"
}`
export const explode = (str: string, maxLength: number, by: string) => {
    let buff = "";
    const numOfLines = Math.floor(str.length / maxLength);
    for (let i = 0; i < numOfLines + 1; i++) {
        buff += str.substr(i * maxLength, maxLength); if (i !== numOfLines) { buff += by; }
    }
    return buff;
}

export const makeCurlCommand = (host:string, url: string, method: string, queries: string, headers: any): string => {
    
    let curl = `curl`
    curl += `\n -X ${method}`        
    try {
        const jsonRequestHeaders = JSON.parse(headers)
        for (const [key, value] of Object.entries(jsonRequestHeaders)) {
            curl += `\n -H "${key}: ${value}"`
        }
    } catch (error: any) {
        curl += `\n -H "Content-Type: application/json"`
    }
    const get = (queries: string) => {
        curl += `\n ${host}/${url}`
        curl += `\n${queries}`
        return curl
    }
    const post = (jsonBody: string) => {
        curl += `\n ${host}/${url}`
        curl += `\n -d '${jsonBody}'`
        return curl
    }
    if (method === "GET" || method === "DELETE" || method === "HEAD") {
        return get(queries)
    }
    if (method === "POST" || method === "PUT" || method === "PATCH") {
        return post(queries)
    }
    return ""
}