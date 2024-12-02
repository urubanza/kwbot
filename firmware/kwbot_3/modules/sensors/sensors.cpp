#include "sensors.h"


sensors sensors::UltrasonicPin(unsigned short int trig, unsigned short int echo) {
	sensors::ultra.echo = echo;
	sensors::ultra.trig = trig;
	return *this;
}

sensors sensors::InfraredPin(unsigned short int analog, unsigned short int digital) {
	sensors::Infrared.analog = analog;
	sensors::Infrared.digital = digital;
	return *this;
}

unsigned short int& sensors::infraredAnalogPin() {
	return sensors::Infrared.analog;
}

float& sensors::ultrasonicMin() {
	return sensors::ultra.Offset.low;
}

float& sensors::infraredMin() {
	return sensors::Infrared.Offset.low;
}

sensors sensors::infraredAnalogPin(unsigned short int pin) {
	sensors::Infrared.analog = pin;
	return *this;
}
sensors sensors::ultrasonicMin(float min) {
	sensors::ultra.Offset.low = min;
	return *this;
}
sensors sensors::infraredMin(float offset) {
	sensors::Infrared.Offset.low = offset;
	return *this;
}

sensors sensors::read() {
	if(sensors::Infrared.on) sensors::Infrared.distance = analogRead(sensors::Infrared.analog);
	if (sensors::ultra.on) {
		digitalWrite(sensors::ultra.trig, LOW);
		delayMicroseconds(2);
		digitalWrite(sensors::ultra.trig, HIGH);
		delayMicroseconds(2);
		digitalWrite(sensors::ultra.trig, LOW);
		sensors::ultra.duration = pulseIn(sensors::ultra.echo, HIGH);
		sensors::ultra.distance = sensors::ultra.duration * 0.034 / 2;
		sensors::ultrasonicAppend(sensors::ultra.distance);
	}

	if (sensors::lineFollow.on) {
		sensors::lineFollow.value = analogRead(sensors::lineFollow.port);
		if (sensors::lineFollow.value < MIN_LINE_FOLLOW && sensors::lineFollow.value < MEAN_LINE_FOLLOW) {
			if (sensors::lineFollow.color == colors::white) {
				sensors::lineFollow.found = true;
			}
			else {
				sensors::lineFollow.found = false;
			}
		}
		else {
			if (sensors::lineFollow.color == colors::white) {
				sensors::lineFollow.found = false;
			}
			else {
				sensors::lineFollow.found = true;
			}
		}
	}

	return *this;
}

void sensors::bazzerBip(unsigned short int dula) {
	for (int val = 0; val < dula; val++) {
		analogWrite(sensors::buzz.pin, (val % 2) * 100);
		delay(10);
	}
	analogWrite(sensors::buzz.pin, 0);
}
void sensors::bazzerFade(unsigned short int dula,unsigned short int sampling) {
	for (int fadeValue = 0; fadeValue <= dula; fadeValue += sampling) {
		analogWrite(sensors::buzz.pin, fadeValue);
		delay(10);
	}
	analogWrite(sensors::buzz.pin, 0);
}

void sensors::bazzerBip() {
	return sensors::bazzerBip(sensors::buzz.duration);
}

void sensors::bazzerFade() {
	return sensors::bazzerFade(sensors::buzz.duration,sensors::buzz.sampling);
}

bool sensors::ultrasonicFound() {
	if (!sensors::ultra.on) return false;
	if (sensors::ultra.distance < sensors::ultra.Offset.low) return true;
	return false;
}
bool sensors::infraredFound() {
	if (!sensors::Infrared.on) return false;
	if (sensors::Infrared.distance < sensors::Infrared.Offset.low) return true;
	return false;
}

sensors sensors::init() {
	// setting the infrared pin to initiation
	pinMode(sensors::Infrared.signal, OUTPUT);
	pinMode(sensors::Infrared.digital, INPUT);
	// setting the ultrasonic pins to initiation
	pinMode(sensors::ultra.trig, OUTPUT);
	pinMode(sensors::ultra.echo, INPUT);
	// setting buzzer pin output
	pinMode(sensors::buzz.pin,OUTPUT);
	return *this;
}

ultrasonic sensors::ultrason() {
	return sensors::ultra;
}

sensors sensors::line(colors c) {
	sensors::lineFollow.color = c;
	return *this;
}

sensors sensors::line(unsigned short int pin, colors c) {
	sensors::lineFollow.color = c;
	sensors::lineFollow.port = pin;
	return *this;
}

sensors sensors::line(unsigned short int pin) {
	sensors::lineFollow.port = pin;
	return *this;
}

lines sensors::line() {
	return sensors::lineFollow;
}

sensors sensors::ultrason(unsigned short int ech, unsigned short int trig) {
	sensors::ultra.echo = ech;
	sensors::ultra.trig = trig;
	return *this;
}

sensors sensors::infra(unsigned short int an, unsigned short int dig, unsigned short int sig) {
	sensors::Infrared.analog = an;
	sensors::Infrared.digital = dig;
	sensors::Infrared.signal = sig;
	return *this;
}

sensors sensors::ActivateUltrasonic() {
	sensors::ultra.on = true;
	return *this;
}

sensors sensors::buzzer(unsigned short int pn, unsigned short int sm, unsigned short int dur) {
	sensors::buzz.pin = pn;
	sensors::buzz.duration = dur;
	sensors::buzz.sampling = sm;
	return *this;
}

sensors sensors::buzzer(unsigned short int pn, unsigned short int sm) {
	sensors::buzz.pin = pn;
	sensors::buzz.sampling = sm;
	return *this;
}

sensors sensors::buzzer(unsigned short int pn) {
	sensors::buzz.pin = pn;
	return *this;
}

void sensors::bip() {
	return sensors::bazzerBip(sensors::buzz.duration);
}

void sensors::fade() {
	return sensors::bazzerFade(sensors::buzz.duration, sensors::buzz.sampling);
}

sensors sensors::ultrasonicAppend(float val) {
	if (sensors::ultra.position == SIZE_OF_LAST_ULTRA_VALUES) {
		sensors::ultra.distances[sensors::ultra.position - 1] = val;
		for (unsigned int i = 0; i < SIZE_OF_LAST_ULTRA_VALUES -1; i++) {
			sensors::ultra.distances[i] = sensors::ultra.distances[i + 1];
		}
	}

	else if (sensors::ultra.position < SIZE_OF_LAST_ULTRA_VALUES) {
		sensors::ultra.distances[sensors::ultra.position] = val;
		sensors::ultra.position++;
	}
	return *this;
}

bool sensors::approaching() {
	if (sensors::ultra.position == 0) return false;
	return true;
}

bool sensors::standing(unsigned int last) {
	if (sensors::ultra.position == 0) return false;
	return true;
}

bool sensors::leaving() {
	if (sensors::ultra.position == 0) return false;
	return true;
}

bool sensors::linefound() {
	return sensors::lineFollow.found;
}


sensors sensors::ultrason(bool v) {
	sensors::ultra.on = v;
	return *this;
}
sensors sensors::infra(bool v) {
	sensors::Infrared.on = v;
	return *this;
}
sensors sensors::line(bool v) {
	sensors::lineFollow.on = v;
	return *this;
}