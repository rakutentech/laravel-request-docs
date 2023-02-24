export interface IAPIRule {
    [key: string]: string[];
}
export interface IAPIInfo {
    uri: string;
    methods: string[];
    middlewares: string[];
    controller: string;
    controller_full_path: string;
    method: string;
    httpMethod: string;
    rules: IAPIRule;
    docBlock: string;
    group: string;
    group_index: number;
    responses: string[];
}