#include "mbed.h"
#include "DHT.h"
#include <Sicet7/Container.h>
#include <Sicet7/To.h>

namespace Sicet7 {
    
    Container* Container::instance = 0;
    Mutex* Container::accessLock = new Mutex();
    
    Container* Container::get() {
        if (Container::instance == 0) {
            Container::accessLock->lock();
            if (Container::instance == 0) {
                Container::instance = new Container();
            }
            Container::accessLock->unlock();
        }
        return Container::instance;
    }
    
    Container::Container() {
        this->instanceLock = new Mutex();
        this->consoleInstance = 0;
        this->networkConnectionInstance = 0;
        this->dhtInstance = 0;
        this->lightSensorInstance = 0;
        this->soundSensorInstance = 0;
        this->tempSensorInstance = 0;
    }
    
    std::string Container::mbedVersion() {
        std::string output = "";
        output.append(To::String(MBED_MAJOR_VERSION));
        output.append(".");
        output.append(To::String(MBED_MINOR_VERSION));
        output.append(".");
        output.append(To::String(MBED_PATCH_VERSION));
        return output;
    }
    
    Console* Container::console() {
        if (this->consoleInstance == 0) {
            this->instanceLock->lock();
            if (this->consoleInstance == 0) {
                this->consoleInstance = new Console();
            }
            this->instanceLock->unlock();
        }
        return this->consoleInstance;
    }
    
    NetworkConnection* Container::networkConnection() {
        if (this->networkConnectionInstance == 0) {
            this->instanceLock->lock();
            if (this->networkConnectionInstance == 0) {
                this->networkConnectionInstance = new NetworkConnection();
            }
            this->instanceLock->unlock();
        }
        return this->networkConnectionInstance;
    }

    DhtThread* Container::dht() {
        if (this->dhtInstance == 0) {
            this->instanceLock->lock();
            if (this->dhtInstance == 0) {
                this->dhtInstance = new DhtThread(D3,DHT22);
            }
            this->instanceLock->unlock();
        }
        return this->dhtInstance;
    }
    
    LightSensor* Container::lightSensor() {
        if (this->lightSensorInstance == 0) {
            this->instanceLock->lock();
            if(this->lightSensorInstance == 0) {
                this->lightSensorInstance = new LightSensor(A3);
            }
            this->instanceLock->unlock();
        }
        return this->lightSensorInstance;
    }
    
    SoundSensor* Container::soundSensor() {
        if (this->soundSensorInstance == 0) {
            this->instanceLock->lock();
            if(this->soundSensorInstance == 0) {
                this->soundSensorInstance = new SoundSensor(A0);
            }
            this->instanceLock->unlock();
        }
        return this->soundSensorInstance;
    }
    
    TempSensor* Container::tempSensor() {
        if (this->tempSensorInstance == 0) {
            this->instanceLock->lock();
            if(this->tempSensorInstance == 0) {
                this->tempSensorInstance = new TempSensor(A1);
            }
            this->instanceLock->unlock();
        }
        return this->tempSensorInstance;
    }
}