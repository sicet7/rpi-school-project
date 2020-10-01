#pragma once
#include "mbed.h"
#include "rtos.h"
#include <DHT.h>

namespace Sicet7 {
    class DhtThread {
        
        private: Thread readThread;
        private: Mutex* writeLock;
        private: DHT* input;
        private: unsigned int reads;
        private: float humidity;
        private: float celsius;
        private: float fahrenheit;
        private: float kelvin;
        
        /**
         * Constructor
         *
         * @param PinName pin
         * @param eType type
         */
        public: DhtThread(PinName pin, eType type);
        
        /**
         * Destructor
         */
        public: ~DhtThread();
        
        /**
         * @return DHT*
         */
        public: DHT* getInput();
        
        /**
         * @param DhtThread* instance
         *
         * @return void
         */
        private: static void process(DhtThread* instance);
        
        /**
         * @return unsigned int
         */
        public: unsigned int getReads();
        
        /**
         * @param unsigned int newValue
         *
         * @return void
         */
        public: void setReads(unsigned int newValue);
        
        /**
         * @return float
         */
        public: float getHumidity();
        
        /**
         * @param bool avg
         *
         * @return float
         */
        public: float getHumidity(bool avg);
        
        /**
         * @param float newValue
         *
         * @return void
         */
        public: void setHumidity(float newValue);
        
        /**
         * @return float
         */
        public: float getCelsius();
        
        /**
         * @param bool avg
         *
         * @return float
         */
        public: float getCelsius(bool avg);
        
        /**
         * @param float newValue
         *
         * @return void
         */
        public: void setCelsius(float newValue);
        
        /**
         * @return float
         */
        public: float getFahrenheit();
        
        /**
         * @param bool avg
         *
         * @return float
         */
        public: float getFahrenheit(bool avg);
        
        /**
         * @param float newValue
         *
         * @return void
         */
        public: void setFahrenheit(float newValue);
        
        /**
         * @return float
         */
        public: float getKelvin();
        
        /**
         * @param bool avg
         *
         * @return float
         */
        public: float getKelvin(bool avg);
        
        /**
         * @param float newValue
         *
         * @return void
         */
        public: void setKelvin(float newValue);
        
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
         * @return void
         */
        public: void reset();
        
    };
}