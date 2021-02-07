import { Injectable } from '@angular/core';

import { environment } from '../../environments/environment';

const url = 'https://maps.googleapis.com/maps/api/js?key=' + environment.gmapKey + '&callback=__onGoogleLoaded&libraries=geometry,drawing';

@Injectable()
export class GmapLoaderService {
    loadAPI: Promise<any>;

    constructor() {
        this.loadAPI = new Promise((resolve) => {
          window['__onGoogleLoaded'] = (ev) => {
            resolve(window['gapi']);
          };
          this.loadScript();
        });
    }

    loadScript() {
        const node = document.createElement('script');
        node.src = url;
        node.type = 'text/javascript';
        document.getElementsByTagName('head')[0].appendChild(node);
    }
}
