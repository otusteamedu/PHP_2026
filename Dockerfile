FROM ubuntu:22.04

WORKDIR /app

COPY sum.sh sort.sh countries.txt ./

RUN chmod +x *.sh

CMD ["bash"]
