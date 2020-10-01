#include <Sicet7/DhtThread.h>
#include <DHT.h>
#ifndef STOP_FLAG
    #define STOP_FLAG 1234
#endif

namespace Sicet7 {
 
    DhtThread::DhtThread(PinName pin, eType type) {
        this->reset();
        this->writeLock = new Mutex();
        this->input = new DHT(pin, type);
        this->readThread.start(callback(DhtThread::process, this));
    }
    
    DhtThread::~DhtThread() {
        this->readThread.flags_set(STOP_FLAG);
        this->readThread.join();
        delete this->writeLock;
        delete this->input;
    }
    
    DHT* DhtThread::getInput() {
        return this->input;
    }
    
    void DhtThread::process(DhtThread* instance) {
        while (!ThisThread::flags_wait_any_for(STOP_FLAG, 1000)) {
            int err = instance->getInput()->readData();
            if (err == ERROR_NONE && instance->trylock()) {
                
                instance->setHumidity(instance->getHumidity() + instance->getInput()->ReadHumidity());
                instance->setCelsius(instance->getCelsius() + instance->getInput()->ReadTemperature(CELCIUS));
                instance->setFahrenheit(instance->getFahrenheit() + instance->getInput()->ReadTemperature(FARENHEIT));
                instance->setKelvin(instance->getKelvin() + instance->getInput()->ReadTemperature(KELVIN));
                instance->setReads(instance->getReads() + 1);
                
                instance->unlock();
            }
        }
    }
    
    unsigned int DhtThread::getReads() {
        return this->reads;
    }
    
    void DhtThread::setReads(unsigned int newValue) {
        this->reads = newValue;
    }
    
    float DhtThread::getHumidity() {
        return this->getHumidity(false);
    }
    
    float DhtThread::getHumidity(bool avg) {
        if (avg) {
            return this->humidity / this->getReads();
        }
        return this->humidity;
    }
    
    void DhtThread::setHumidity(float newValue) {
        this->humidity = newValue;
    }
    
    float DhtThread::getCelsius() {
        return this->getCelsius(false);
    }
    
    float DhtThread::getCelsius(bool avg) {
        if (avg) {
            return this->celsius / this->getReads();
        }
        return this->celsius;
    }
    
    void DhtThread::setCelsius(float newValue) {
        this->celsius = newValue;
    }
    
    float DhtThread::getFahrenheit() {
        return this->getFahrenheit(false);
    }
    
    float DhtThread::getFahrenheit(bool avg) {
        if (avg) {
            return this->fahrenheit / this->getReads();
        }
        return this->fahrenheit;
    }
    
    void DhtThread::setFahrenheit(float newValue) {
        this->fahrenheit = newValue;
    }
    
    float DhtThread::getKelvin() {
        return this->getKelvin(false);
    }
    
    float DhtThread::getKelvin(bool avg) {
        if (avg) {
            return this->kelvin / this->getReads();
        }
        return this->kelvin;
    }
    
    void DhtThread::setKelvin(float newValue) {
        this->kelvin = newValue;
    }
    
    void DhtThread::lock() {
        this->writeLock->lock();
    }
    
    bool DhtThread::trylock() {
        return this->writeLock->trylock();
    }
    
    void DhtThread::unlock() {
        this->writeLock->unlock();
    }
    
    void DhtThread::reset() {
        this->reads = 0;
        this->humidity = 0.0f;
        this->celsius = 0.0f;
        this->fahrenheit = 0.0f;
        this->kelvin = 0.0f;
    }
}