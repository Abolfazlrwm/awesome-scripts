CXX = g++
CXXFLAGS = -std=c++17 -O2
LDFLAGS = -lcurl
TARGET = rss-news-bot

all: $(TARGET)

$(TARGET): main.cpp
	$(CXX) $(CXXFLAGS) main.cpp -o $(TARGET) $(LDFLAGS)

clean:
	rm -f $(TARGET)

.PHONY: all clean
