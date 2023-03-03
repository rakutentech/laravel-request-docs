export interface IAPIRule {
    [key: string]: string[];
}
export interface IAPIInfo {
    uri: string;
    middlewares: string[];
    controller: string;
    controller_full_path: string;
    method: string;
    http_method: string;
    rules: IAPIRule;
    docBlock: string;
    group: string;
    group_index: number;
    responses: string[];
}
