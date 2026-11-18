import { duracionEnMinutos, textoDeDuracion } from './duracion';

describe('duración de una reserva', () => {
  it('cuenta los minutos entre el inicio y el fin', () => {
    expect(duracionEnMinutos('2027-03-04T09:30', '2027-03-04T11:00')).toBe(90);
  });
  it('escribe las horas y los minutos', () => {
    expect(textoDeDuracion(90)).toBe('1 h 30 min');
  });
  it('omite los minutos cuando la duración es de horas enteras', () => {
    expect(textoDeDuracion(120)).toBe('2 h');
  });
  it('omite las horas cuando no llega a una', () => {
    expect(textoDeDuracion(45)).toBe('45 min');
  });
});
