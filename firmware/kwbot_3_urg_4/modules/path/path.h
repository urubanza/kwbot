#pragma once
#include<math.h>
#include"timer.cpp"

enum  length {mm , cm, m , km};




struct valueTime
{
	STRING_ value = "";
	STRING_ time = "";
	int ends = 0;
};
struct point  {
	//point(int i, int j, int k) : x(i), j(){}
	// the point and of the position where a robot is located
	double x = 0, y = 0, z = 0;
	unsigned short int mesure:2;
	//char a;
	//int b : 5,
	//	c : 11,
	//	: 0,
	//	d : 8;
	//struct { int ee : 8; } e;
};

struct pathread {
	double speed = 0;
	unsigned int angle = 90;
	double speedTime = 0;
	double angleTime = 0;
	point location;
};

class path
{
	private:
		// a variable to keep the direction of the vehicle this is the angle in degree where 
		// the vehicle will be oriented posible values are from 0 to 359
		// where 90 is forward, 0 is right 180 is left then 270 is backward
		unsigned int Tdirection:9;
		// a variable to keep a velocity in m/s
		double velocity;
		// a variable to keep time where the this point happened
		double Ttime;
		//  a variable to keep the acceleration of the car
		double acceleration = 0;
		// a variable to keep distance at this point in point format.
		point distance;
		// a function to calculate the multiple of a distance
		float multiple();
		// an array of motors to drive
		motors *motor[2];
		timer TIMER = timer(100);
		
		
		// estimated time to be used by the path
		float estimatedTime = 0;
		// the used time of the path at current time
		float elapsedTime = 0;
		// the length of the path in form of number of angles
		float length = 0;
		// the current position we are on
		float position = 0;
		// the scale to keep all informations about all 
		float screenGroundScale = 1 / 1;

		const char *anglesBorders =  "()" ;
		const char *speedBorders = "{}";
		const char *pointBorder = "[]";
		const char *grobals = "<>";

		STRING_ message = "";
		bool endedx = false;

	public:
		bool ended(){
			return path::endedx;
		}
		// a constructor with forward direction, velocity of 1.00 m/s and Ttime of 0.00 sec 
		path() : Tdirection(90), velocity(1.00), Ttime(0.00) { }
		// a constructor with provided direction
		path(int x):Tdirection(x), velocity(1.00), Ttime(0.00) {}
		// a constructor with provided direction, velocity and time 
		path(int x, double v, double t) :Tdirection(x), velocity(v), Ttime(t) {}
		// a constructor with also point added 
		path(int x, double v, double t,point p) :Tdirection(x), velocity(v), Ttime(t), distance(p) {}
		// a constructor with only point provided
		path(point p): distance(p), Tdirection(90), velocity(1.00), Ttime(0.00){}
		// a constructor with point, velocity and time provided
		path(point p, double v, double t) : distance(p), Tdirection(90), velocity(v), Ttime(t) {}
		// a function to return the direction 
		unsigned int direction();
		//  a function to set the direction
		path direction(unsigned int);
		// a getter of the velocity
		double speed();
		// a setter of a velocity
		path speed(double);
		// a getter of distance
		point  location();
		// a setter of a distance
		path location(point);
		// a setter of a distance for mesurements
		path location(unsigned short);

		// a setter of a distance for mesurements
		path location(double,double,double);
		// a getter of time
		double time();
		// a setter of a time
		path time(double);
		// a function to make a car move forward
		path forward();
		path backward();
		path turnLeft();
		path turnRight();
		path turnRight(unsigned int dir);
		path turnLeft(unsigned int dir);
		path TurnLeft(unsigned int dir);
		path TurnRight(unsigned int dir);
		path TurnLeft();
		path TurnRight();
		path Stop();
		path turn(float);
		path accerelate(double);
		double accelerate();
		double &decerelate();
		// a function to add motors 
		path Motors(motors*, motors*);
		path Drive(bool,bool,double, double);

		static int angleDeg(float);
		static float angleRad(int);
		path hindukira(bool);
		path ready(double, void (*)(path), void (*)(timer));
		timer tim();
		path restartTimer(double);
		path read();
		pathread read(bool);
		STRING_* valuePair(STRING_, char, char);
		path load(STRING_);
		STRING_ msg();
		valueTime valuePairO(STRING_, char, char);
		virtual ~path(){}
};

