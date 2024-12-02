#pragma once
#define SIZE_OF_LAST_ULTRA_VALUES 1000

#define MIN_LINE_FOLLOW 50
#define MEAN_LINE_FOLLOW 500
#define MAX_LINE_FOLLOW 1023

#define OFF false
#define ON true

// the line follower color value
enum class colors { white = 0, black = 1};
// a struct that will used for switching


// a the offset structure to keep low and high value of offset
struct offset {
	float low;
	float high;
	offset() {
		offset::high = 0;
		offset::low = 0;
	}
	offset(float h, float l) {
		offset::high = h;
		offset::low = l;
	}
	~offset() {};
};

// a structure to save the ultrasonic
struct ultrasonic {
	float duration;
	float distance;
	float distances[SIZE_OF_LAST_ULTRA_VALUES] = {};
	float times[SIZE_OF_LAST_ULTRA_VALUES] = {};
	// a variable to determine the the position we are on in the datastructure of distances
	unsigned int position;
	offset Offset;
	bool on;
	// pin definition
	unsigned short int echo = 0;
	unsigned short int trig = 0;
	bool obstacle;
	ultrasonic(unsigned short int echo, unsigned short int trig){
		ultrasonic::duration = 0;
		ultrasonic::distance = 0;
		ultrasonic::on = false;
		ultrasonic::echo = echo;
		ultrasonic::trig = trig;
		ultrasonic::obstacle = false;
		ultrasonic::position = 0;
		for (unsigned int i = 0; i < SIZE_OF_LAST_ULTRA_VALUES; i++) {
			ultrasonic::distances[i] = 0;
			ultrasonic::times[i] = 0;
		}
	}

	ultrasonic() {
		ultrasonic::duration = 0;
		ultrasonic::distance = 0;
		ultrasonic::on = false;
		ultrasonic::echo = 0;
		ultrasonic::trig = 0;
		ultrasonic::obstacle = false;
		ultrasonic::position = 0;
		for (unsigned int i = 0; i < SIZE_OF_LAST_ULTRA_VALUES; i++) {
			ultrasonic::distances[i] = 0;
			ultrasonic::times[i] = 0;
		}
		
	}

	~ultrasonic(){}

};


struct infrared {
	offset Offset;
	bool on;
	// a definition of pins
	unsigned short int analog;
	unsigned short int digital;
	unsigned short int signal;
	// end of definition of pins
	float distance;
	bool obstacle;

	infrared(unsigned short int an, unsigned short int dig, unsigned short int sig) {
		infrared::Offset = offset();
		infrared::on = false;
		infrared::obstacle  = false;
		infrared::analog = an;
		infrared::digital = dig;
		infrared::distance = 0;
		infrared::signal = sig;
		
	}

	infrared() {
		infrared::Offset = offset();
		infrared::on = false;
		infrared::obstacle = false;
		infrared::analog = 51;
		infrared::digital = 0;
		infrared::distance = 0;
		infrared::signal = 49;
	}

	~infrared() {};

};

struct buzzer {

	unsigned short int duration;
	unsigned short int pin : 5;
	unsigned short int sampling;
	bool on;
	buzzer() {
		buzzer::on = false;
		buzzer::duration = 10;
		buzzer::pin = 10;
		buzzer::sampling = 20;
	}

	buzzer(unsigned short int pn,unsigned short int samp, unsigned short int dur) {
		buzzer::on = false;
		buzzer::duration = dur;
		buzzer::pin = pn;
		buzzer::sampling = samp;
	}
	~buzzer(){}
};
// the structure for all required data for line following
struct lines {
	unsigned short int port;
	unsigned int value;
	bool found;
	colors color;
	bool on;
	float width;
	lines(): port(A5), value(0), found(false), color(colors::white), on(false), width(5) {};
	virtual ~lines() {};
};

class sensors
{
private:
	// definition of sensor to be provided by sensors
	ultrasonic ultra;
	infrared Infrared;
	buzzer buzz;
	lines lineFollow;
	// a function to append the value to the ultrasonic list of values
	sensors ultrasonicAppend(float);
	

public:
	sensors() : Infrared(50,51,49),
				ultra(52,53),
				buzz(),
				lineFollow(){};
	// the setter of infrared and ultrasonic respectively
	sensors ultrason(unsigned short int, unsigned short int);
	sensors infra(unsigned short int, unsigned short int, unsigned short int);
	sensors line(unsigned short int,colors);
	sensors line(unsigned short int);
	sensors line(colors);
	// the functions for switching all sensors
	sensors ultrason(bool);
	sensors infra(bool);
	sensors line(bool);
	//a setter of ultrasonic senor pin where the first arg is for trriger and the ech for the second
	sensors UltrasonicPin(unsigned short int, unsigned short int);
	// a seeter of infrared where the first is arg is analog and the second one digital
	sensors InfraredPin(unsigned short int, unsigned short int);
	// the initiator of the whole sensor reader
	sensors init();
	// a function to set infrared analog pin
	unsigned short int& infraredAnalogPin();
	// the getter of the minimum range for detection on ultrasonic
	float& ultrasonicMin();
	//the getter of the minimum range of the detection of infrared
	float& infraredMin();

	sensors infraredAnalogPin(unsigned short int);
	sensors ultrasonicMin(float);
	sensors infraredMin(float);

	sensors read();
	static char* string2char(STRING_ command) {
		if (command.length() != 0) {
			char* p = const_cast<char*>(command.c_str());
			return p;
		}
	}
	void  bazzerBip(unsigned short int);
	void bazzerFade(unsigned short int,unsigned short int);
	void bazzerBip();
	void bazzerFade();
	void bip();
	void fade();
	sensors buzzer(unsigned short int,unsigned short int,unsigned short int);
	sensors buzzer(unsigned short int, unsigned short int);
	sensors buzzer(unsigned short int);
	bool ultrasonicFound();
	bool infraredFound();
	virtual ~sensors(){}

	// a function to read the obsatcle distance in number

	ultrasonic ultrason();

	sensors ActivateUltrasonic();

	lines line();
	//a function to check if the obstacle is approaching
	bool approaching();
	bool standing(unsigned int);
	bool leaving();
};