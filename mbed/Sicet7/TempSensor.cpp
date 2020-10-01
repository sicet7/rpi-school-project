#include <Sicet7/TempSensor.h>
#ifndef STOP_FLAG
    #define STOP_FLAG 1234
#endif

namespace Sicet7 {
    
    TempSensor::TempSensor(PinName pin) {
        this->sum = 0.0f;
        this->reads = 0;
        this->writeLock = new Mutex();
        this->input = new AnalogIn(pin);
        this->readThread.start(callback(TempSensor::process, this));
    }
    
    TempSensor::~TempSensor() {
        this->readThread.flags_set(STOP_FLAG);
        this->readThread.join();
        delete this->writeLock;
        delete this->input;
    }
    
    AnalogIn* TempSensor::getInput() {
        return this->input;
    }
    
    float TempSensor::getSum() {
        return this->sum;
    }
    
    unsigned int TempSensor::getReads() {
        return this->reads;
    }
    
    void TempSensor::setSum(float newValue) {
        this->sum = newValue;
    }
    
    void TempSensor::setReads(unsigned int newValue) {
        this->reads = newValue;
    }
    
    void TempSensor::lock() {
        this->writeLock->lock();
    }
    
    bool TempSensor::trylock() {
        return this->writeLock->trylock();
    }
    
    void TempSensor::unlock() {
        this->writeLock->unlock();
    }
    
    void TempSensor::process(TempSensor* instance) {
        while (!ThisThread::flags_wait_any_for(STOP_FLAG, 10)) {
            if (instance->trylock()) {
                instance->setSum(instance->getSum() + instance->getInput()->read());
                instance->setReads(instance->getReads() + 1);
                instance->unlock();
            }
        }
    }
    
    float TempSensor::readOutput() {
        this->lock();
        float output = this->getSum() / this->getReads();
        this->setSum(0.0f);
        this->setReads(0);
        this->unlock();
        return output;
    }
}