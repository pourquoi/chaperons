import { Injectable } from '@angular/core';

import { Observable } from 'rxjs/Observable';
import '../rxjs.operators.ts'

import { ApiService } from './api';

import { User } from '../models/user';

@Injectable()
export class AuthService {

  _auth_token: any;
  user: User;
  redirectUrl: string;

  constructor( private apiService: ApiService ) {
    const token = localStorage.getItem('auth_token');
    if (token) {
      this.auth_token = JSON.parse(token);
    }
  }

  get auth_token(): any {
    return this._auth_token;
  }

  set auth_token(token) {
    this._auth_token = token;
    if (token) {
        this.apiService.apiKey = token.api_key;
    } else {
        this.apiService.apiKey = null;
    }
  }

  get loggedin(): boolean {
    return !!(this.auth_token);
  }

  login(username, password): Observable<any> {
    const source = this.apiService.login(username, password);
    source.subscribe(
        (user) => {
          this.auth_token = {user_id: user.id, api_key: user.api_key};
          this.user = user;
          localStorage.setItem('auth_token', JSON.stringify(this.auth_token));
        },
        (err) => {
        });
    return source;
  }

  logout(): void {
    this.auth_token = null;
    this.user = null;
    localStorage.removeItem('auth_token');
  }

  loadUser(): Observable<any>|boolean {

    if (this.user) {
      return Observable.of(this.user);
    }

    if (this.auth_token) {
        const source = this.apiService.getResource('/users/' + this.auth_token.user_id);

        source.subscribe(
          user => this.user = user,
          err => {
            this.logout();
          }
        );
        return source;
    }

    return false;
  }

}


