import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { BarraDeAvisos } from './avisos/barra-de-avisos/barra-de-avisos';

@Component({
  imports: [RouterOutlet, RouterLink, RouterLinkActive, BarraDeAvisos],
  selector: 'app-root',
  styleUrl: './app.css',
  templateUrl: './app.html',
})
export class App {}
