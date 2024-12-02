#ifndef CONFIG_H_INCLUDED
#define CONFIG_H_INCLUDED

/// card number will be a std::string in UNIX like and String in arduino framework
//#define STRING_ std::string
#define STRING_ String
// String in arduino IDE
#define SUBSTR substring
// substr in std lib
#define DOUBLE(x) String(x).toDouble()
// std::stod(x) for std lib
#define VALUE_STRING(x) String(x)
// std::to_string(x) for standard lib
#define SUBSTING(x,y) substring(x,y)
// substr(x,y) for arduino

#define ENA 14
#define ENB 12
#define IN_1 15
#define IN_2 13
#define IN_3 2
#define IN_4 0
//#define WHEEL_DIAMETER 90
#define PI 3.14159265359
#define CAR_RADIUS 49
#define MAX_SPEED 1.33


/*##############################################################################################################
 * // variables for network connectivity
 ###############################################################################################################*/

//const char *ssid = "Didy Cub";
//const char *password = "Didy123";
const char *socketServer = "192.168.1.71";

const char *ssid = "CANALBOX-D47A";
const char *password = "9472845084";

//const char *socketServer = "172.16.20.200";
const int socketPort = 8080;

 /// grobal variables for device identification for assigned building on it and this device_id in the 
 /// main server database according to the serial number assigned on this device
const char *robot_serial = "00000327";
unsigned long company_id = 0;
unsigned long robot_id = 0;
unsigned short int newDecice = 0;


/// the inline function to validate card used number so that all spaces between card number will be removed
inline STRING_ Validate(STRING_ In){
    STRING_ new_v = "";
    for(unsigned short int i=0;i<In.length();i++){
        if(!(STRING_(In[i])==STRING_(" "))){
           new_v = new_v + STRING_(In[i]);
        }
    }
    return new_v;
}
#endif // CONFIG_H_INCLUDED
