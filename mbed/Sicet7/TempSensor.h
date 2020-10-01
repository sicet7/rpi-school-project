#pragma once
#include "mbed.h"
#include "rtos.h"

namespace Sicet7 {
    class TempSensor {
        
        private: Thread readThread;
        private: Mutex* writeLock;
        private: AnalogIn* input;
        private: float sum;
        private: unsigned int reads;
        
        /**
         * @param TempSensor* instance
         *
         * @return void
         */
        private: static void process(TempSensor* instance);
        
        /**
         * Constructor
         *
         * @param PinName pin
         */
        public: TempSensor(PinName pin);
        
        /**
         * Destructor
         */
        public: ~TempSensor();
        
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