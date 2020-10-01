#include "mbed.h"
#include "rtos.h"
#include "http_request.h"
#include "https_request.h"
#include <cmath>
#include <sstream>
#include <DHT.h>
#include <Sicet7/Container.h>
#include <Sicet7/Console.h>
#include <Sicet7/NetworkConnection.h>
#include <Sicet7/To.h>
#include <Sicet7/LightSensor.h>
#include <Sicet7/SoundSensor.h>
#include <Sicet7/TempSensor.h>
#include <Sicet7/DhtThread.h>

//Change these options
#define MY_PATH "/api/entry"
#define MY_SERVER "10.130.54.33"
#define MY_AUTH_HEADER "bearer 0123456789"
#define WAIT_TIME 5000
#define USE_HTTPS 0

using namespace Sicet7;

#if USE_HTTPS > 0
    #define MY_HTTP_HOST "https://" MY_SERVER MY_PATH
    const char SSL_CA_PEM[] = "-----BEGIN CERTIFICATE-----\n"
"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\n"
"-----END CERTIFICATE-----\n";
#else
    #define MY_HTTP_HOST "http://" MY_SERVER MY_PATH
#endif

#define STOP_FLAG 1234

int main()
{
    //Sensors
    DhtThread* dht = Container::get()->dht();
    SoundSensor* sound = Container::get()->soundSensor();
    LightSensor* light = Container::get()->lightSensor();
    TempSensor* temp = Container::get()->tempSensor();
    
    //Console container.
    Console* console = Container::get()->console();
    
    //Get network connection container.
    NetworkConnection* network = Container::get()->networkConnection();

    // OS Version on startup.
    console->writeLine("-----------------------");
    console->write("Mbed OS Version: ");
    console->writeLine(Container::get()->mbedVersion());
    
    std::string payload = "";
    
    while(true) {
        
        // Wait
        ThisThread::sleep_for(WAIT_TIME);
        
        //Seperate console output.
        console->writeLine("-----------------------");
        
        //Reset payload.
        payload.assign("");
        
        //make sure we have analog data on the output.
        if (sound->getReads() <= 0 || temp->getReads() <= 0 || light->getReads() <= 0) {
            console->writeLine("Skipping due to missing analog data.");
            continue;
        }
        
        //make sure we have digital data on the output
        if (dht->getReads() <= 0) {
            console->writeLine("Skipping due to missing digital data.");
            continue;
        }
        
        //Check if network is connected.
        if(!network->isConnected()) {
            console->writeLine("Failed to connect to network, connect Ethernet cable and Restart to try again.");
            break;
        } else {
            console->write("Connected to network with IP: ");
            console->writeLine(network->getIpAddress());
        }
        
        
        //Build payload.
        payload.append("{");
        
        payload.append("\"Sound\":\"");
        payload.append(To::String(sound->readOutput()));
        payload.append("\",");
        
        payload.append("\"Temp\":\"");
        payload.append(To::String(temp->readOutput()));
        payload.append("\",");
        
        payload.append("\"Light\":\"");
        payload.append(To::String(light->readOutput()));
        payload.append("\",");
        
        dht->lock();
        
        payload.append("\"Humidity\":\"");
        payload.append(To::String(dht->getHumidity(true)));
        payload.append("\",");
        
        payload.append("\"Celsius\":\"");
        payload.append(To::String(dht->getCelsius(true)));
        payload.append("\",");
        
        payload.append("\"Fahrenheit\":\"");
        payload.append(To::String(dht->getFahrenheit(true)));
        payload.append("\",");
        
        payload.append("\"Kelvin\":\"");
        payload.append(To::String(dht->getKelvin(true)));
        payload.append("\"");
        
        dht->reset();
        dht->unlock();
        
        payload.append("}");
        
        //Create Request
        #if USE_HTTPS > 0
            HttpsRequest* req = new HttpsRequest(network->getInterface(), SSL_CA_PEM, HTTP_POST, MY_HTTP_HOST);
        #else
            HttpRequest* req = new HttpRequest(network->getInterface(), HTTP_POST, MY_HTTP_HOST);
        #endif
        
        //Set headers on request
        req->set_header("Content-Type", "application/json");
        req->set_header("Authorization", MY_AUTH_HEADER);
        
        //Send request.
        HttpResponse* res = req->send(payload.c_str(), payload.length());
        
        if (res->get_status_code() != 200 && res->get_status_code() != 204) {
            console->writeLine("HTTP request failed.");
            if (res->get_status_code() < 100 || res->get_status_code() > 599) {
                console->write("Could not connect to remote server: ");
                console->writeLine(MY_HTTP_HOST);
            } else {
                console->write("Return Code: ");
                console->writeLine(To::String(res->get_status_code()));
            }
        } else {
            console->write("HTTP request was successful and returned code: ");
            console->writeLine(To::String(res->get_status_code()));
            console->write("Payload: ");
            console->writeLine(payload);
        }
        
        //Clean up.
        delete req;
    }
}
