import { Injectable, computed, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class Avisos {
  private readonly texto = signal<string | null>(null);
  readonly actual = this.texto.asReadonly();
  readonly hayAviso = computed(() => this.texto() !== null);

  mostrar(texto: string): void {
    this.texto.set(texto);
    setTimeout(() => this.texto.set(null), 5000);
  }
}
