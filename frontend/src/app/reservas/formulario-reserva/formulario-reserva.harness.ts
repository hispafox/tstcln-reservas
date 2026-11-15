import { ComponentHarness, TestElement } from '@angular/cdk/testing';

export class FormularioReservaHarness extends ComponentHarness {
  static hostSelector = 'app-formulario-reserva';

  private readonly persona = this.locatorFor('#persona');
  private readonly inicio = this.locatorFor('#inicio');
  private readonly fin = this.locatorFor('#fin');
  private readonly boton = this.locatorFor('button[type="submit"]');
  private readonly avisos = this.locatorForAll('[role="alert"]');

  async rellenar(datos: {
    persona: string;
    inicio: string;
    fin: string;
  }): Promise<void> {
    await escribir(await this.persona(), datos.persona);
    await escribir(await this.inicio(), datos.inicio);
    await escribir(await this.fin(), datos.fin);
  }

  async puedeReservar(): Promise<boolean> {
    return !(await (await this.boton()).getProperty<boolean>('disabled'));
  }

  async mensajes(): Promise<string[]> {
    return Promise.all((await this.avisos()).map((aviso) => aviso.text()));
  }
}

async function escribir(campo: TestElement, valor: string): Promise<void> {
  await campo.setInputValue(valor);
  await campo.dispatchEvent('input');
}
