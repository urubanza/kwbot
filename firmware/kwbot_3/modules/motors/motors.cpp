#include "motors.h"
motors motors::speedPort(unsigned short int port) {
	motors::EN_PORT = port;
	return *this;
}

unsigned short int &motors::speedPort() {
	return motors::EN_PORT;
}

motors& motors::input1(unsigned short int port) {
	motors::IN_PORT1 = port;
	return *this;
}

unsigned short int& motors::input1() {
	return motors::IN_PORT1;
}
motors motors::input2(unsigned short int port) {
	motors::IN_PORT2 = port;
	return *this;
}

unsigned short int& motors::input2() {
	return motors::IN_PORT2;
}

motors motors::diameter(double dia) {
	motors::WHEEL_DIAMETER = dia;
	return *this;
}

double& motors::diameter() {
	return motors::WHEEL_DIAMETER;
}
motors motors::type(unsigned int type) {
	if (type > 1)
		type = 1;
	motors::TYPE = type;
	return *this;
}

motors motors::minVoltage(float vol) {
	motors::MIN_VOLTAGE = vol;
	return *this;
}

motors motors::maxVoltage(float vol) {
	motors::MAX_VOLTAGE = vol;
	return *this;
}

motors motors::rpm(float rp) {
	motors::RPM = rp;
	return *this;
}

motors motors::stop() {
	digitalWrite(motors::IN_PORT1, LOW);
	digitalWrite(motors::IN_PORT2, LOW);
	analogWrite(motors::EN_PORT, motors::speedTo8bit(0));
	return *this;
}

motors motors::run(double speed, float duration) {
	digitalWrite(motors::IN_PORT1, HIGH);
	digitalWrite(motors::IN_PORT2, LOW);
	analogWrite(motors::EN_PORT, motors::speedTo8bit(speed));
	return *this;
}
motors motors::back(double speed, float duration) {
	digitalWrite(motors::IN_PORT1,LOW);
	digitalWrite(motors::IN_PORT2,HIGH);
	analogWrite(motors::EN_PORT,motors::speedTo8bit(speed));
	return *this;
}

unsigned short int motors::speedTo8bit(double one) {
	double rets = (one / 1.33) * DAC_RESOLUTION;
	motors::DAC_VALUE = int(rets);
	return motors::DAC_VALUE;
}

motors motors::init() {
	pinMode(motors::EN_PORT, OUTPUT);
	pinMode(motors::IN_PORT1, OUTPUT);
	pinMode(motors::IN_PORT2, OUTPUT);
	return *this;
}