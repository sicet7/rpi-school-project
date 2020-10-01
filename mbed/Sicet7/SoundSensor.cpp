#include <Sicet7/SoundSensor.h>
#ifndef STOP_FLAG
    #define STOP_FLAG 1234
#endif

namespace Sicet7 {
    
    SoundSensor::SoundSensor(PinName pin) {
        this->sum = 0;
        this->reads = 0;
        this->writeLock = new Mutex();
        this->input = new AnalogIn(pin);
        this->readThread.start(callback(SoundSensor::process, this));
    }
    
    SoundSensor::~SoundSensor() {
        this->readThread.flags_set(STOP_FLAG);
        this->readThread.join();
        delete this->writeLock;
        delete this->input;
    }
    
    AnalogIn* SoundSensor::getInput() {
        return this->input;
    }
    
    unsigned int SoundSensor::getSum() {
        return this->sum;
    }
    
    unsigned int SoundSensor::getReads() {
        return this->reads;
    }
    
    void SoundSensor::setSum(unsigned int newValue) {
        this->sum = newValue;
    }
    
    void SoundSensor::setReads(unsigned int newValue) {
        this->reads = newValue;
    }
    
    void SoundSensor::lock() {
        this->writeLock->lock();
    }
    
    bool SoundSensor::trylock() {
        return this->writeLock->trylock();
    }
    
    void SoundSensor::unlock() {
        this->writeLock->unlock();
    }
    
    void SoundSensor::process(SoundSensor* instance) {
        while (!ThisThread::flags_wait_any_for(STOP_FLAG, 10)) {
            if (instance->trylock()) {
                unsigned int sum = 0;
                for(int i = 0; i < 1000; i++) {
                    sum += instance->getInput()->read_u16();
                }
                instance->setSum(instance->getSum() + (sum/1000));
                instance->setReads(instance->getReads() + 1);
                instance->unlock();
            }
        }
    }
    
    float SoundSensor::readOutput() {
        this->lock();
        float output = this->getSum() / this->getReads();
        this->setSum(0);
        this->setReads(0);
        this->unlock();
        return output;
    }
    
}