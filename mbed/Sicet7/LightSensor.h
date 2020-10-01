#pragma once
#include "mbed.h"
#include "rtos.h"

namespace Sicet7 {
    class LightSensor {
        
        private: Thread readThread;
        private: Mutex* writeLock;
        private: AnalogIn* input;
        private: float sum;
        private: unsigned int reads;
        
        /**
         * @param LightSensor* instance
         *
         * @return void
         */
        private: static void process(LightSensor* instance);
        
        /**
         * Constructor
         *
         * @param PinName pin
         */
        public: LightSensor(PinName pin);
        
        /**
         * Destructor
         */
        public: ~LightSensor();
        
        /**
         * @return AnalogIn*
         */
        public: AnalogIn* getInput();
        
        /**
         * @return float
         */
        public: float getSum();
        
        /**
         * @return unsigned int
         */
        public: unsigned int getReads();
        
        /**
         * @param float newValue
         *
         * @return void
         */
        public: void setSum(float newValue);
        
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