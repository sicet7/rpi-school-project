#pragma once
#include "mbed.h"
#include "rtos.h"

namespace Sicet7 {
    class SoundSensor {
        
        private: Thread readThread;
        private: Mutex* writeLock;
        private: AnalogIn* input;
        private: unsigned int sum;
        private: unsigned int reads;
        
        /**
         * @param SoundSensor* instance
         *
         * @return void
         */
        private: static void process(SoundSensor* instance);
        
        /**
         * Constructor
         *
         * @param PinName pin
         */
        public: SoundSensor(PinName pin);
        
        /**
         * Destructor
         */
        public: ~SoundSensor();
        
        /**
         * @return AnalogIn*
         */
        public: AnalogIn* getInput();
        
        /**
         * @return unsigned int
         */
        public: unsigned int getSum();
        
        /**
         * @return unsigned int
         */
        public: unsigned int getReads();
        
        /**
         * @param unsigned int newValue
         *
         * @return void
         */
        public: void setSum(unsigned int newValue);
        
        /**
         * @param unsigned int newValue
         *
         * @return void
         */
        public: void setReads(unsigned int newValue);
        
        /**
         * @return void
         */
        public: void lock();
        
        /**
         * @return bool
         */
        public: bool trylock();
        
        /**
         * @return void
         */
        public: void unlock();
        
        /**
         * @return float
         */
        public: float readOutput();
    };
}