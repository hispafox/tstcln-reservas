import { TestBed } from '@angular/core/testing';
import { firstValueFrom, of } from 'rxjs';
import { nombres, sala } from '../../testing/datos-de-prueba';
import { ApiSalas } from './datos/api-salas';
import { CatalogoSalas } from './catalogo-salas';

describe('CatalogoSalas', () => {
  const salas = [
    sala({ nombre: 'Norte', capacidadMaxima: 4 }),
    sala({ nombre: 'Sur', capacidadMaxima: 10 }),
    sala({ nombre: 'Este', capacidadMaxima: 20 }),
  ];

  beforeEach(() => {
    const api = { listar: vi.fn(() => of(salas)) };
    TestBed.configureTestingModule({
      providers: [{ provide: ApiSalas, useValue: api }],
    });
  });

  it('devuelve solo las salas donde entran los asistentes', async () => {
    const catalogo = TestBed.inject(CatalogoSalas);

    const disponibles = await firstValueFrom(
      catalogo.disponibles({ asistentes: 8, equipamiento: [] }),
    );

    expect(nombres(disponibles)).toEqual(['Sur', 'Este']);
  });
});
