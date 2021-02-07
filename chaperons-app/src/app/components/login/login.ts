import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-login',
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class LoginComponent implements OnInit {

  username: string = '';
  password: string = '';

  error: string = '';
  connecting = false;

  constructor(public authService: AuthService, public router: Router, private location:Location) { 
    
  }

  ngOnInit() {
    this.username = '';
    this.password = '';
    this.error = '';

    this.authService.logout();

    // cosmetic when the url is /logout
    this.location.replaceState('/login');
  }

  login() {
    if( this.connecting ) return false;

    this.connecting = true;
    this.authService.login(this.username, this.password).subscribe(
      user => {
        console.log('logged in cmpt');
        let redirect = this.authService.redirectUrl ? this.authService.redirectUrl : '/maps';
        this.router.navigate([redirect]);
        this.connecting = false;
      },
      error => {
        this.error = error; 
        this.connecting = false;
      },
      () => this.connecting = false
    )
  }

}
