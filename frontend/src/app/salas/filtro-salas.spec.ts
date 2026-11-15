import { nombres, sala } from '../../testing/datos-de-prueba';
import { filtrarSalasDisponibles } from './filtro-salas';

describe('filtrarSalasDisponibles', () => {
  const norte = sala({ nombre: 'Norte', capacidadMaxima: 4 });
  const sur = sala({ nombre: 'Sur', capacidadMaxima: 10, equipamiento: ['pantalla'] });
  const auditorio = sala({ nombre: 'Auditorio', capacidadMaxima: 40, capacidadMinima: 12 });
  const salas = [norte, sur, auditorio];

  it('deja fuera las salas donde no entran los asistentes', () => {
    expect(nombres(filtrarSalasDisponibles(
      salas, { asistentes: 8, equipamiento: [] },
    )))
      .toEqual(['Sur']);
  });

  it('deja fuera las salas que exigen más asistentes de los que vienen', () => {
    expect(nombres(filtrarSalasDisponibles(salas, { asistentes: 10, equipamiento: [] })))
      .toEqual(['Sur']);
  });

  it('admite el número exacto de la capacidad máxima', () => {
    expect(nombres(filtrarSalasDisponibles(salas, { asistentes: 4, equipamiento: [] })))
      .toEqual(['Norte', 'Sur']);
  });

  it('admite el número exacto de la capacidad mínima', () => {
    expect(nombres(filtrarSalasDisponibles(salas, { asistentes: 12, equipamiento: [] })))
      .toEqual(['Auditorio']);
  });

  it('exige todo el equipamiento pedido', () => {
    expect(nombres(filtrarSalasDisponibles(salas, { asistentes: 2, equipamiento: ['pantalla'] })))
      .toEqual(['Sur']);
  });
});
