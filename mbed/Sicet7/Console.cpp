#include "mbed.h"
#include <Sicet7/Console.h>

namespace Sicet7 {
    Console::Console() {
        this->instance = new Serial(USBTX, USBRX, 9600);
        this->writeMutex = new Mutex();
    }
    
    void Console::writeWithLock(const char* output) {
        this->instance->printf(output);
    }
    
    void Console::write(const char* output) {
        this->writeMutex->lock();
        this->writeWithLock(output);
        this->writeMutex->unlock();
    }
    
    void Console::writeLine(const char* output) {
        this->writeMutex->lock();
        this->writeWithLock(output);
        this->writeWithLock("\n\r");
        this->writeMutex->unlock();
    }
    
    void Console::write(const std::string* output) {
        this->write(output->c_str());
    }
    
    void Console::writeLine(const std::string* output) {
        this->writeLine(output->c_str());
    }
    
    void Console::write(const std::string output) {
        this->write(&output);
    }
    
    void Console::writeLine(const std::string output) {
        this->writeLine(&output);
    }
}