#pragma once
class timer
{
	private:
		// declaration of the member values in millseconds
		double initialTime, endTime;
	public:
		timer(double end) : initialTime(0) { endTime = end; }
		// a function to check the progress of the timer
		double progress(double);
		// a function to check if the timer has expired
		bool expired(double);
		// a function to restart the timer
		timer restart(double);
		// a function to update the timer end time
		timer end(double);
		// a function to expand the timer to the given value
		timer expand(double);
		virtual ~timer() {};

};

