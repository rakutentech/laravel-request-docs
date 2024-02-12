export interface IAPIRule {
    [key: string]: string[];
}

export interface IConfig {
    title: string;
    default_headers: string[];
}

export interface IAPIInfo {
    uri: string;
    middlewares: string[];
    controller: string;
    controller_full_path: string;
    method: string;
    http_method: string;
    rules: IAPIRule;
    path_parameters: IAPIRule;
    doc_block: string;
    group: string;
    group_index: number;
    responses: string[];

}

export interface LRDResponse {
    data: unknown,
    _lrd: {
        queries: [],
        logs: {
            level: string,
            message: string,
            context: [],
        }[],
        models: [],
        modelsTimeline: [],
        memory: string,
    }
}
