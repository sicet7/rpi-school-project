#include <Sicet7/To.h>
#include <sstream>
namespace Sicet7 {
    std::string To::String(const float floatVar) {
        std::ostringstream ss;
        ss << floatVar;
        return ss.str();
    }
    std::string To::String(const int intVar) {
        std::ostringstream ss;
        ss << intVar;
        return ss.str();
    }
    std::string To::String(const unsigned int intVar) {
        std::ostringstream ss;
        ss << intVar;
        return ss.str();
    }
}