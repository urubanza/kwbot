#pragma once
#define DAC_RESOLUTION 1023
// a class to control the motors pin and activities
class motors
{
private:
	// enable port from the motor speed controller
	unsigned short int EN_PORT;
	// 2 ports for the input ports
	unsigned short int IN_PORT1;
	unsigned short int IN_PORT2;
	// the diameter of the Wheel in mm
	double WHEEL_DIAMETER;
	// the type of the motor where 1 will be that the bigger value the less speep and zero the bigger value the higher speed
	unsigned int TYPE : 1;
	// the minimum and maximum value of voltage to be applied on the the motor to start
	float MIN_VOLTAGE;
	float MAX_VOLTAGE;
	// the rpm of the motor in round per minutes
	float RPM;
public:
	unsigned short int DAC_VALUE = 0;
	motors(): TYPE(0){}
	motors speedPort(unsigned short int);
	unsigned short int& speedPort();
	motors& input1(unsigned short int);
	unsigned short int& input1();
	motors input2(unsigned short int);
	unsigned short int& input2();
	motors diameter(double);
	double &diameter();
	motors type(unsigned int);
	motors minVoltage(float);
	motors maxVoltage(float);
	motors rpm(float);
	motors stop();
	motors run(double,float);
	motors back(double,float);
	unsigned short int speedTo8bit(double);
	motors init();
	virtual ~motors(){}
};

