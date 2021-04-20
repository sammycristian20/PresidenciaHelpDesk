'use strict';

/**
 * A constructor function for the timer service
 * @param { Function } callback callback function to execute after some `delay`
 * @param { Number } delay How many second to delay the function exection
 * @param { Boolean } canUserStopTimer can user able to stop the timer or not?
 * @param { Boolean } canPauseResume can user pause/resume the timer?
 * @param { Boolean } showTimer falg to show/hide timer
 */
export function Timer(callback, delay, canUserStopTimer, canPauseResume = true, showTimer = true) {
  this.timerId;
  this.remaining = delay;
  this.canUserStopTimer = canUserStopTimer;
  this.canPauseResume = canPauseResume;
  this.showTimer = showTimer;

  // Pause the timer service
  this.pause = function () {
    // Clear timer instance if paused
    clearTimeout(this.timerId);
  };

  // Returns the remaining time left
  this.getRemaining = function () {
    return this.remaining;
  };

  // Resume the timer service
  this.resume = function () {
    clearTimeout(this.timerId);
    this.timerId = setInterval(() => {
      this.remaining -= 1;
      if (this.remaining <= 0) {
        clearTimeout(this.timerId);
        callback();
      }
    }, 1000);
  };

  // Start the timer as soon as the Timer instance is created 
  this.resume();
};
