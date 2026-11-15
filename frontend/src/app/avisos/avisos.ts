import { Injectable, computed, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class Avisos {
  private readonly texto = signal<string | null>(null);
  readonly actual = this.texto.asReadonly();
  readonly hayAviso = computed(() => this.texto() !== null);

  private temporizador: ReturnType<typeof setTimeout> | undefined;

  mostrar(texto: string): void {
    clearTimeout(this.temporizador);
    this.texto.set(texto);
    this.temporizador = setTimeout(() => this.texto.set(null), 5000);
  }
}
