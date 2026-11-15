import { TestBed } from '@angular/core/testing';
import { Avisos } from '../avisos';
import { BarraDeAvisos } from './barra-de-avisos';

describe('BarraDeAvisos', () => {
  it('enseña el aviso en pantalla', async () => {
    const fixture = TestBed.createComponent(BarraDeAvisos);
    const avisos = TestBed.inject(Avisos);

    avisos.mostrar('Reserva creada');
    await fixture.whenStable();

    expect(fixture.nativeElement.textContent).toContain('Reserva creada');
  });
});
