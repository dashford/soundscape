FROM alpine:3.11
RUN apk add --upgrade sqlite
RUN mkdir /db
WORKDIR /db

ENTRYPOINT ["sqlite3"]
CMD ["soundscape.db"]