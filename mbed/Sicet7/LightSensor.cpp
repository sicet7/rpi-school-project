#include <Sicet7/LightSensor.h>
#ifndef STOP_FLAG
    #define STOP_FLAG 1234
#endif

namespace Sicet7 {
    
    LightSensor::LightSensor(PinName pin) {
        this->sum = 0.0f;
        this->reads = 0;
        this->writeLock = new Mutex();
        this->input = new AnalogIn(pin);
        this->readThread.start(callback(LightSensor::process, this));
    }
    
    LightSensor::~LightSensor() {
        this->readThread.flags_set(STOP_FLAG);
        this->readThread.join();
        delete this->writeLock;
        delete this->input;
    }
    
    AnalogIn* LightSensor::getInput() {
        return this->input;
    }
    
    float LightSensor::getSum() {
        return this->sum;
    }
    
    unsigned int LightSensor::getReads() {
        return this->reads;
    }
    
    void LightSensor::setSum(float newValue) {
        this->sum = newValue;
    }
    
    void LightSensor::setReads(unsigned int newValue) {
        this->reads = newValue;
    }
    
    void LightSensor::lock() {
        this->writeLock->lock();
    }
    
    bool LightSensor::trylock() {
        return this->writeLock->trylock();
    }
    
    void LightSensor::unlock() {
        this->writeLock->unlock();
    }
    
    void LightSensor::process(LightSensor* instance) {
        while (!ThisThread::flags_wait_any_for(STOP_FLAG, 10)) {
            if (instance->trylock()) {
                instance->setSum(instance->getSum() + instance->getInput()->read());
                instance->setReads(instance->getReads() + 1);
                instance->unlock();
            }
        }
    }
    
    float LightSensor::readOutput() {
        this->lock();
        float output = this->getSum() / this->getReads();
        this->setSum(0.0f);
        this->setReads(0);
        this->unlock();
        return output;
    }
}