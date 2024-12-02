#include "timer.h"

double timer::progress(double current) {
	return (current-timer::initialTime)/timer::endTime;
}

bool timer::expired(double current) {
	return (current - timer::initialTime) > timer::endTime;
}

timer timer::restart(double current) {
	timer::initialTime = current;
	return *this;
}

timer timer::end(double ends) {
	timer::endTime = ends;
	return *this;
}

timer timer::expand(double add) {
	timer::endTime += add;
	return *this;
}


