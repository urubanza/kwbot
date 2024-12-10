#include "path.h"
unsigned int path::direction() {
	return path::Tdirection;
}

path path::direction(unsigned int d) {
	while (d >= 360) {
		d -= 360;
	}
	path::Tdirection = d;
	return *this;
}

path path::speed(double S) {
	path::velocity = S;
	return *this;
}

double path::speed() {
	return path::velocity;
}

point path::location() {
	return path::distance;
}

path path::location(point d) {
	path::distance.x = d.x * path::multiple();
	path::distance.y = d.y * path::multiple();
	path::distance.z = d.z * path::multiple();
	return *this;
}

path path::location(unsigned short mes) {
	if (mes > 3) {
		path::distance.mesure = 0;
	}
	else {
		path::distance.mesure = mes;
	}
	return *this;
}

path path::location(double x, double y, double z) {
	path::distance.x = x * path::multiple();
	path::distance.y = y * path::multiple();
	path::distance.z = z * path::multiple();
	return *this;
}

double path::time() {
	return path::Ttime;
}

path path::time(double t) {
	path::Ttime = t;
	return *this;
}

path path::forward() {
	path::direction(90);
	path::motor[0]->run(path::velocity, path::time());
	path::motor[1]->run(path::velocity, path::time());
	path::TIMER.end(path::time());
	return *this;
}
path path::backward() {
	path::direction(270);
	path::motor[0]->back(path::velocity, path::time());
	path::motor[1]->back(path::velocity, path::time());
	path::TIMER.end(path::time());
	return *this;
}
path path::turnLeft() {
	path::direction(0);
	path::motor[0]->run(path::velocity, path::time());
	path::motor[1]->back(path::velocity, path::time());
	path::TIMER.end(path::time());
	return *this;
}

path path::turnRight() {
	path::direction(180);
	path::motor[0]->run(path::velocity, path::time());
	path::motor[1]->back(path::velocity, path::time());
	path::TIMER.end(path::time());
	return *this;
}

path path::turnRight(unsigned int dir) {
	path::direction(dir);
	double arc = ((PI * path::Tdirection) / 180) * CAR_RADIUS;
	double times = ((arc / MAX_SPEED) / 100) * 1000;
	path::time(times / 2);
	path::turnRight();
	path::TIMER.end(path::time());
	return *this;
}

path path::turnLeft(unsigned int dir) {
	path::direction(dir);
	double arc = ((PI * path::Tdirection) / 180) * CAR_RADIUS;
	double times = ((arc / MAX_SPEED) / 100) * 1000;
	path::time(times / 2);
	path::turnLeft();
	path::TIMER.end(path::time());
	return *this;
}

path path::TurnRight(unsigned int dir) {
	path::direction(dir);
	double arc = ((PI * path::Tdirection) / 180) * CAR_RADIUS;
	double times = ((arc / MAX_SPEED) / 100) * 1000;
	path::time(times);
	path::TurnRight();
	path::TIMER.end(path::time());
	return *this;
}

path path::TurnLeft(unsigned int dir) {
	path::direction(dir);
	double arc = ((PI * path::Tdirection) / 180) * CAR_RADIUS;
	double times = ((arc / MAX_SPEED) / 100) * 1000;
	path::time(times);
	path::TurnLeft();
	path::TIMER.end(path::time());
	return *this;
}

path path::TurnLeft() {
	path::direction(180);
	path::motor[0]->run(path::velocity, path::time());
	path::motor[1]->stop();
	path::TIMER.end(path::time());
	return *this;
}

path path::TurnRight() {
	path::direction(0);
	path::motor[0]->stop();
	path::motor[1]->run(path::velocity, path::time());
	path::TIMER.end(path::time());
	return *this;
}

float path::multiple() {
	float multiple = 1;
	switch (path::distance.mesure)
	{
	case 0: {
		multiple = 1 / 1000;
		break;
	}
	case 1: {
		multiple = 1 / 100;
		break;
	}
	case 2: {
		multiple = 1;
		break;
	}
	case 3: {
		multiple = 1000;
		break;
	}
	default:
		break;
	}
	return multiple;
}

path path::accerelate(double acc) {
	path::acceleration = acc;
	return *this;
}

double& path::decerelate() {
	return path::acceleration;
}

double path::accelerate() {
	return path::acceleration;
}

path path::Drive(bool dir1, bool dir2, double speed1, double speed2) {
	if (dir1) {
		path::motor[0]->run(speed1, 1000);
	}
	else path::motor[0]->back(speed1, 1000);
	if (dir2) motor[1]->run(speed2, 1000);
	else motor[1]->back(speed2, 1000);
}

path path::Motors(motors* first, motors* second) {
	path::motor[0] = first;
	path::motor[1] = second;
	return *this;
}

path path::Stop() {
	path::direction(0);
	path::motor[0]->stop();
	path::motor[1]->stop();
	return *this;
}

path path::turn(float angle) {
	int angleChange = path::direction() - path::angleDeg(angle);
	path::direction(path::angleDeg(angle));
	float arcLength = (CAR_RADIUS * angle)/100;
	float duration = arcLength / path::speed();
	if (angle < (PI / 2)) {
		path::motor[0]->run(path::speed() * sin(angle), duration);
		path::motor[1]->run(path::speed(), duration);
	}
	else if (angle < PI) {
		path::motor[0]->run(path::speed(), duration);
		path::motor[1]->run(path::speed() * sin(angle), duration);
	}
	else if (angle < (3 * PI)/4) {
		path::motor[0]->back(path::speed() * sin(angle) * (-1), duration);
		path::motor[1]->back(path::speed(), duration);
	}
	else {
		path::motor[0]->back(path::speed(), duration);
		path::motor[1]->back(path::speed() * sin(angle)*(-1), duration);
	}
	path::time(duration);
	return *this;
}

int path::angleDeg(float degs) {
	return (degs * 180) / PI;
}
float path::angleRad(int degs) {
	return (degs * PI) / 180;
}

path path::rotate(bool left) {
	if (left) {
		path::motor[0]->back(path::speed(), 10);
		path::motor[1]->run(path::speed(), 10);
	}
	else {
		path::motor[1]->back(path::speed(), 10);
		path::motor[0]->run(path::speed(), 10);
	}
	return *this;
}

path path::ready(double current, void (*conts)(path x), void (*waiting)(timer y)) {
	if (TIMER.expired(current)) {
		(*conts)(*this);
	}
	else {
		(*waiting)(path::TIMER);
	}
}

timer path::tim() {
	return path::TIMER;
}

path path::restartTimer(double t) {
	path::TIMER.restart(t);
	return *this;
}

path path::read() {
	unsigned short int whatToRead = 0;
	valueTime speed;
	valueTime angle;
	valueTime points;
	for (unsigned int ii = 0; ii < path::message.length(); ii++) {
		if (path::message.SUBSTING(ii,ii+1) == String(path::anglesBorders[0])) {
			whatToRead = 1;
		}
		else if (path::message.SUBSTING(ii,ii+1) == String(path::anglesBorders[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::speedBorders[0])) {
			whatToRead = 2;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::speedBorders[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::pointBorder[0])) {
			whatToRead = 3;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::pointBorder[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::grobals[0])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::grobals[1])) {
			path::load(path::message.SUBSTING(ii+1,path::message.length()));
			break;
		}
		else {
			switch (whatToRead)
			{
			case 0 :{
				break;
			}
			case 1: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii,path::message.length()), path::anglesBorders[0], path::anglesBorders[1]);
				//angle.time = res[1];
				//angle.value = res[0];
				angle = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::anglesBorders[0], path::anglesBorders[1]);
				ii += angle.ends;
				break;
			}
			case 2: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii, path::message.length()), path::speedBorders[0], path::speedBorders[1]);
				//speed.time = res[1];
				//speed.value = res[0];
				speed = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::speedBorders[0], path::speedBorders[1]);
				ii += angle.ends;
				break;
			}
			case 3: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii, path::message.length()), path::pointBorder[0], path::pointBorder[1]);
				//points.time = res[1];
				//points.value = res[0];
				points = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::pointBorder[0], path::pointBorder[1]);
				ii += angle.ends;
				break;
			}
			default:
				break;
			}
		}
	}
	
	path::direction(path::angleDeg(DOUBLE(angle.value)));
	path::speed(DOUBLE(speed.value));
	//this->speed(DOUBLE(speed.value));
	path::time(DOUBLE(angle.time));
	path::location(DOUBLE(points.time), DOUBLE(points.value), 0);
	path::position++;
	return *this;
}
pathread path::read(bool reads) {
	unsigned short int whatToRead = 0;
	valueTime speed;
	valueTime angle;
	valueTime points;
	pathread rets;
	for (unsigned int ii = 0; ii < path::message.length(); ii++) {
		//path::message.SUBSTING(ii, ii + 1);
		if (path::message.SUBSTING(ii, ii + 1) == String(path::anglesBorders[0])) {
			whatToRead = 1;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::anglesBorders[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::speedBorders[0])) {
			whatToRead = 2;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::speedBorders[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::pointBorder[0])) {
			whatToRead = 3;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::pointBorder[1])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::grobals[0])) {
			whatToRead = 0;
		}
		else if (path::message.SUBSTING(ii, ii + 1) == String(path::grobals[1])) {
			path::load(path::message.SUBSTING(ii + 1, path::message.length()));
			if(path::msg().length() == 0){
				path::endedx = true;
			}
			break;
		}
		else {
			switch (whatToRead)
			{
			case 0: {
				break;
			}
			case 1: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii,path::message.length()), path::anglesBorders[0], path::anglesBorders[1]);
				//angle.time = res[1];
				//angle.value = res[0];
				angle = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::anglesBorders[0], path::anglesBorders[1]);
				ii += angle.ends;
				break;
			}
			case 2: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii, path::message.length()), path::speedBorders[0], path::speedBorders[1]);
				//speed.time = res[1];
				//speed.value = res[0];
				speed = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::speedBorders[0], path::speedBorders[1]);
				ii += angle.ends;
				break;
			}
			case 3: {
				//STRING_* res = path::valuePair(path::message.SUBSTING(ii, path::message.length()), path::pointBorder[0], path::pointBorder[1]);
				//points.time = res[1];
				//points.value = res[0];
				points = path::valuePairO(path::message.SUBSTING(ii, path::message.length()), path::pointBorder[0], path::pointBorder[1]);
				ii += angle.ends;
				break;
			}
			default:
				break;
			}
		}

		rets.angle = path::angleDeg(DOUBLE(angle.value));
		rets.speed = DOUBLE(speed.value);
		rets.speedTime = DOUBLE(angle.time);
		rets.angleTime = DOUBLE(angle.time);
		rets.location.x = DOUBLE(points.time);
		rets.location.y = DOUBLE(points.value);
		path::position++;
	}
	return rets;
}

STRING_ *path::valuePair(STRING_ str, char start, char ends) {
	unsigned short int pos = 0;
	STRING_ all[2] = {"",""};

	for (unsigned int ii = 0; ii < str.length(); ii++) {
		if (str.SUBSTING(ii,ii+1) == String(start)) {
			break;
		}
		else if (str.SUBSTING(ii, ii + 1) == String(ends)) {
			break;
		}
		else if (str.SUBSTING(ii, ii + 1) == String('-')) {
			pos++;
		}
		else {
			if (pos > 0) {
				all[0] = all[0] + String(str[ii]);
			}
			else {
				all[1] = all[1] + String(str[ii]);
			}
		}
	}
	return all;
}
valueTime path::valuePairO(STRING_ str, char start, char ends) {
	unsigned short int pos = 1;
	valueTime all;

	for (unsigned int ii = 0; ii < str.length(); ii++) {
		if (str.SUBSTING(ii, ii + 1) == String(start)) {
			break;
		}
		else if (str.SUBSTING(ii, ii + 1) == String(ends)) {
			all.ends = ii;
			break;
		}
		else if (str.SUBSTING(ii, ii + 1) == String('-')) {
			pos--;
		}
		else {
			if (pos > 0) {
				all.value = all.value + String(str[ii]);
			}
			else {
				all.time = all.time + String(str[ii]);
			}
		}
	}
	return all;
}

path path::load(STRING_ s) {
	path::message = s;
	return *this;
}
STRING_ path::msg() {
	return path::message;
}