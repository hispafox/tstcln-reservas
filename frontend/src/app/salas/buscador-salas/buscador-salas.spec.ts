import { TestBed } from '@angular/core/testing';
import { of } from 'rxjs';
import { sala } from '../../../testing/datos-de-prueba';
import { CatalogoSalas } from '../catalogo-salas';
import { BuscadorSalas } from './buscador-salas';

describe('BuscadorSalas', () => {
  const salas = [
    sala({ nombre: 'Norte', capacidadMaxima: 4 }),
    sala({ nombre: 'Sur', capacidadMaxima: 10 }),
    sala({ nombre: 'Este', capacidadMaxima: 20 }),
  ];

  it('muestra las salas que encuentra el catálogo', async () => {
    TestBed.configureTestingModule({
      imports: [BuscadorSalas],
      providers: [{ provide: CatalogoSalas, useValue: { disponibles: vi.fn(() => of(salas)) } }],
    });

    const fixture = TestBed.createComponent(BuscadorSalas);
    await fixture.whenStable();

    expect(fixture.nativeElement.textContent).toContain('Sur');
  });
});
