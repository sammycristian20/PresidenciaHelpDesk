import { Timer } from '../../views/js/utils';

describe('Timer', () => {

  jest.useFakeTimers();

  it('Timer constructor will initalize its instance variables', () => {
    let timer1 = new Timer(() => 3, 2, true, true, true);
    timer1.resume = jest.fn();
    expect(timer1.timerId).not.toBe(undefined);
    expect(timer1.remaining).toBe(2);
    expect(timer1.canUserStopTimer).toBe(true);
    expect(timer1.canPauseResume).toBe(true);
    expect(timer1.showTimer).toBe(true);

    let timer2 = new Timer(() => 3, 2, false);
    expect(timer2.timerId).not.toBe(undefined);
    expect(timer2.remaining).toBe(2);
    expect(timer2.canUserStopTimer).toBe(false);
    expect(timer2.canPauseResume).toBe(true);
    expect(timer2.showTimer).toBe(true);
  })

  it('pause will clear Timer instance', () => {
    let timer = new Timer(() => 3, 2, false);
    timer.pause();
    expect(clearTimeout).toHaveBeenCalledWith(timer.timerId);
  })

  it('getRemaining will return remaining time left', () => {
    let timer = new Timer(() => 3, 2, false);
    expect(timer.getRemaining()).toBe(2);
  })

  it('calling resume function will decrement the remaining time', () => {
    const callback = jest.fn();
    let timer = new Timer(callback, 2, false);

    // At this point in time, the callback should not have been called yet
    expect(callback).not.toBeCalled();
    expect(timer.getRemaining()).toBe(2);

    // Fast-forward until all timers have been executed
    jest.runAllTimers();

    // Now our callback should have been called!
    expect(callback).toBeCalled();
    expect(callback).toHaveBeenCalledTimes(1);
    expect(timer.getRemaining()).toBe(0);
  })
})