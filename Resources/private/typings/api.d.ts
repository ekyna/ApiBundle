declare module Ekyna {
    export interface Api {
        init(route: string);
    }
}

declare let Api:Ekyna.Api;

declare module 'ekyna-api' {
    export = Api;
}
