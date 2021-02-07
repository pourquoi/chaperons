import { Injectable, Inject } from '@angular/core';
import { HttpClient, HttpHeaders, HttpResponse } from '@angular/common/http';
import { Observable } from 'rxjs/Observable';
import '../rxjs.operators';
import { environment } from '../../environments/environment';

@Injectable()
export class ApiService {

  private _endpoint: string;
  private _apiKey: string;
  private cache = {};

  constructor( private http: HttpClient) {
    this._endpoint = environment.apiEndpoint;
  }

  get apiKey(): string {
    return this._apiKey;
  }

  set apiKey(key: string) {
      this._apiKey = key;
  }

  get endpoint(): string {
    return this._endpoint;
  }

  set endpoint(url: string) {
    this._endpoint = url;
  }

  login(username, password): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });

    const body = JSON.stringify({username, password});

    const options = {headers};

    return this.http.post(this.endpoint + '/users/logins', body, options)
      .catch(this.handleError)
      .share();
  }

  clearCache(path): void {
      if (path === undefined) {
        this.cache = {};
      } else if (path in this.cache) {
          delete this.cache[path];
      }
  }

  getResource(path, cache = false): Observable<any> {
    console.log('get resource: ' + path);

    if ( cache && path in this.cache ) {
      return Observable.of(this.cache[path]).share();
    }

    const headers = {
      'Content-Type': 'application/json',
      'X-TEST': this.apiKey
    };

    if ( this.apiKey ) {
        headers['X-AUTH-TOKEN'] = this.apiKey;
    }

    return this.http.get(this.endpoint + path, {headers})
      .map((res) => {
        if ( cache ) {
            this.cache[path] = res;
        }
        return res;
      })
      .catch(this.handleError)
      .share();
  }

  postResource(path, data, method = 'POST'): Observable<any> {
    const headers = {
      'Content-Type': 'application/json'
    };

    if ( this.apiKey ) {
        headers['X-AUTH-TOKEN'] = this.apiKey;
    }

    const options = {headers};

    const body = JSON.stringify(data);

    let f;

    if (method === 'POST') {
        return this.http.post(this.endpoint + path, body, options)
          .catch(this.handleError)
          .share();
    } else if (method === 'PATCH') {
        return this.http.patch(this.endpoint + path, body, options)
          .catch(this.handleError)
          .share();
    } else if (method === 'PUT') {
        return this.http.put(this.endpoint + path, body, options)
          .catch(this.handleError)
          .share();
    }
  }

  deleteResource(path) {
    const headers = {
      'Content-Type': 'application/json'
    };

    if ( this.apiKey ) {
        headers['X-AUTH-TOKEN'] = this.apiKey;
    }

    const options = {headers};

    return this.http.delete(this.endpoint + path, options)
      .catch(this.handleError)
      .share();
  }

  private handleError(error: any) {
    console.log(error);
    return Observable.throw(error);
  }

}
